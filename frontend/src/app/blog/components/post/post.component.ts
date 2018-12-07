import {Component, ComponentFactoryResolver, OnInit, Renderer2, ViewChild, ViewContainerRef} from '@angular/core';
import {BlogService} from '../../services/blog.service';
import {ActivatedRoute} from '@angular/router';
import {SpinnerTagComponent} from '../spinner-tag/spinner-tag.component';
import {AuthService} from '../../../http/auth/auth.service';
import {flatMap} from 'rxjs/operators';

@Component({
  selector: 'app-post',
  templateUrl: './post.component.html',
  styleUrls: ['./post.component.sass']
})
export class PostComponent implements OnInit {
  public post: any;
  spinnerTag = false;
  isLiked = false;
  countLikes: any = 0;
  userName = '';

  @ViewChild('spinnerTagWrap', {read: ViewContainerRef}) viewContainerRefLike: ViewContainerRef;

  constructor(
    private componentFactoryResolver: ComponentFactoryResolver,
    private _blogService: BlogService,
    private _activatedRoute: ActivatedRoute,
    private render: Renderer2,
    private _authService: AuthService,
  ) { }

  ngOnInit() {
    const slug = this._activatedRoute.snapshot.params['slug'];

    this._authService.isLogin()
      .pipe(
        flatMap( token => {
          if (!!token) {
            this.userName = token.username;
          }
          return this._blogService.getPost(slug);
        })
      ).subscribe( post => {
      this.post = post;
      console.log(post);
      if (this.post.hasOwnProperty('likedBy') && this.post.likedBy.length) {
        this.isLiked = true;
        this.countLikes = this.post.likedBy.length;
        this.isLiked = this.findObjectByKey(this.post.likedBy, 'username', this.userName);
        // console.log(this.countLikes);
      }
    });
  }
  like(event) {
    console.log(event.target);
    if (!this.post.hasOwnProperty('id')) {
      return false;
    }
    const componentFactory = this.componentFactoryResolver.resolveComponentFactory(SpinnerTagComponent);
    const spinnerComponent =  this.viewContainerRefLike.createComponent(componentFactory);
    this.spinnerTag = true;
    this.render.appendChild(event.target, spinnerComponent.location.nativeElement);
    this._blogService.postLike(this.post.id).subscribe(status => {
      if (status.status) {
        this.spinnerTag = false;
        spinnerComponent.destroy();
        this.countLikes = status.message;
        this.isLiked = true;
      } else {
        console.log(status.message);
        spinnerComponent.destroy();
      }
    });
    return false;
  }
  unLike(event) {
    if (!this.post.hasOwnProperty('id')) {
      return false;
    }
    const componentFactory = this.componentFactoryResolver.resolveComponentFactory(SpinnerTagComponent);
    const spinnerComponent =  this.viewContainerRefLike.createComponent(componentFactory);
    this.spinnerTag = true;
    this.render.appendChild(event.target, spinnerComponent.location.nativeElement);
    this._blogService.postUnLike(this.post.id).subscribe(status => {
      this.spinnerTag = false;
      spinnerComponent.destroy();
      if (status.status) {
        this.countLikes = status.message;
        this.isLiked = false;
      } else {
        console.log(status.message);
        spinnerComponent.destroy();
      }
    });
    return false;
  }
  findObjectByKey(array, key, value) {
    for (let i = 0; i < array.length; i++) {
      if (array[i][key] === value) {
        // return array[i];
        return true;
      }
    }
    return false;
  }
}
