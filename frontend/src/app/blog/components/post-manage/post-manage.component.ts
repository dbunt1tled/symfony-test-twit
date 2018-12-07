import {Component, OnInit} from '@angular/core';
import {BlogService} from '../../services/blog.service';
import {ActivatedRoute, Router} from '@angular/router';
import {AuthService} from '../../../http/auth/auth.service';
import {flatMap} from 'rxjs/operators';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {Post} from '../../models/blog/post';
import {FlashMessagesService} from 'angular2-flash-messages';

@Component({
  selector: 'app-post-manage',
  templateUrl: './post-manage.component.html',
  styleUrls: ['./post-manage.component.sass']
})
export class PostManageComponent implements OnInit {
  post: Post;
  categories: [];
  userName = '';
  managePost: FormGroup;
  minSymbols = 3;
  submitted = false;
  constructor(
    private _blogService: BlogService,
    private _activatedRoute: ActivatedRoute,
    private _authService: AuthService,
    private _flashMessage: FlashMessagesService,
    private _router: Router,
    private _fb: FormBuilder,
  ) { }

  ngOnInit() {
    const slug = this._activatedRoute.snapshot.params['slug'];

    this.managePost =  this._fb.group({
      text: ['', [Validators.required, Validators.minLength(this.minSymbols)]],
      title: ['', [Validators.required, Validators.minLength(this.minSymbols)]],
      category: ['', [Validators.required, Validators.minLength(this.minSymbols)]],
      slug: ['', [Validators.required, Validators.minLength(this.minSymbols)]],
      enabled: ['', [Validators.required]],
    });

    this._authService.isLogin()
      .pipe(
        flatMap( token => {
          if (!!token) {
            this.userName = token.username;
          }
          return this._blogService.getPostForManage(slug);
        })
      ).subscribe( data => {
      this.post = data.post;
      this.categories = data.categories;
      this.managePost.setValue({
        text: this.post.text,
        title: this.post.title,
        slug: this.post.slug,
        enabled: this.post.enabled,
        category: this.post.category.id
      });
    });
  }
  get getField() { return this.managePost.controls; }
  onSubmit(event) {
    event.preventDefault();
    this.submitted = true;
    if (this.managePost.invalid) {
      console.log(this.getFormValidationErrors());
      return;
    }
    const valuesForm = this.managePost.value;
    const post = Object.assign(this.post, valuesForm);
    console.log(post);
    this._blogService.getPostUpdate(post).subscribe( status => {
      if (status.status) {
        this._router.navigate(['', this.post.slug]);
      } else {
        const error = status.message.toString().split('::');
        if (error.length === 1) {
          this._flashMessage.show(status.message.toString(),
            {cssClass: 'alert-danger', closeOnClick: true, showCloseBtn: true, timeout: 3000 });
        } else if (error.length === 2) {
          this._flashMessage.show('Wrong manage data',
            {cssClass: 'alert-danger', closeOnClick: true, showCloseBtn: true, timeout: 3000 });
          if (this.managePost.controls[error[0]]) {
            this.managePost.controls[error[0]].setErrors({'incorrect': error[1]});
          } else {
            console.log('Error: ' + error[0] + ' (' + error[1] + ')');
          }
        }
        console.log('Wrong edit data');
      }
    });
  }
  getFormValidationErrors() {
    Object.keys(this.managePost.controls).forEach(key => {

      const controlErrors = this.managePost.get(key).errors;
      if (controlErrors != null) {
        Object.keys(controlErrors).forEach(keyError => {
          console.log('Key control: ' + key + ', keyError: ' + keyError + ', err value: ', controlErrors[keyError]);
        });
      }
    });
  }
}
