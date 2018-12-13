import {Component, OnInit} from '@angular/core';
import {BlogService} from '../../services/blog.service';
import {ActivatedRoute, Router} from '@angular/router';
import {AuthService} from '../../../http/auth/auth.service';
import {flatMap} from 'rxjs/operators';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {Post} from '../../models/blog/post';
import {FlashMessagesService} from 'angular2-flash-messages';
import {of} from 'rxjs';

@Component({
  selector: 'app-post-manage',
  templateUrl: './post-manage.component.html',
  styleUrls: ['./post-manage.component.sass']
})
export class PostManageComponent implements OnInit {
  slug = '';
  post: Post;
  categories: [];
  userName = '';
  managePost: FormGroup;
  minSymbols = 6;
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
    this.slug = this._activatedRoute.snapshot.params['slug'];

    this.managePost =  this._fb.group({
      text: ['', [Validators.required, Validators.minLength(this.minSymbols)]],
      title: ['', [Validators.required, Validators.minLength(this.minSymbols)]],
      category: ['', [Validators.required, Validators.minLength(this.minSymbols)]],
      slug: ['', [/*Validators.required,/**/ Validators.minLength(this.minSymbols)]],
      enabled: ['', [/*Validators.required/**/]],
    });

    this._authService.isLogin()
      .pipe(
        flatMap( token => {
          if (!!token) {
            this.userName = token.username;
          }
          if (this.slug) {
            return this._blogService.getPostForManage(this.slug);
          }
          //return of(false);
          return this._blogService.getCategoriesDropDown();
        })
      ).subscribe( data => {
      if (this.slug) {
        this.post = data.post;
        this.categories = data.categories;
        this.managePost.setValue({
          text: this.post.text,
          title: this.post.title,
          slug: this.post.slug,
          enabled: this.post.enabled,
          category: this.post.category.id
        });
      } else {
        this.categories = data.categories;
      }
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
    const post  = this.managePost.value;

    if (this.slug) {
      this.updatePost(post, this.slug);
    } else {
      console.log(post);
      this.addPost(post);
    }
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
  updatePost(post: Post, slug: string) {
    this._blogService.postUpdate(slug, post).subscribe( status => {
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
  addPost(post: Post) {
    this._blogService.postAdd(post).subscribe( status => {
      if (status.status) {
        this._router.navigate(['', status.message]);
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
}
