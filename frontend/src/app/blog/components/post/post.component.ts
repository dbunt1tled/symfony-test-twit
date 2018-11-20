import {Component, ComponentFactoryResolver, OnInit, Renderer2, ViewChild, ViewContainerRef} from '@angular/core';
import {BlogService} from '../../services/blog.service';
import {ActivatedRoute} from '@angular/router';
import {SpinnerTagComponent} from '../spinner-tag/spinner-tag.component';
import {AuthService} from '../../../http/auth/auth.service';
import {flatMap} from 'rxjs/operators';
import {of} from 'rxjs';

@Component({
  selector: 'app-post',
  templateUrl: './post.component.html',
  styleUrls: ['./post.component.sass']
})
export class PostComponent implements OnInit {
  post: any;
  spinnerTag: boolean = false;
  isLiked: boolean = false;
  countLikes: any = 0;
  userName: string = '';

  @ViewChild('spinnerTagWrap', {read: ViewContainerRef}) viewContainerRefLike: ViewContainerRef;

  constructor(
    private componentFactoryResolver: ComponentFactoryResolver,
    private _blogService: BlogService,
    private _activatedRoute: ActivatedRoute,
    private render: Renderer2,
    private _authService: AuthService,
  ) { }

  ngOnInit() {
    let slug = this._activatedRoute.snapshot.params['slug'];

    this._authService.isLogin()
      .pipe(
        flatMap( token =>{
          if(!!token){
            this.userName = token.username;
            return this._blogService.getPost(slug);
          }
          return of(false)
        })
      ).subscribe( post =>{
      this.post = post;
      console.log(post);
      if(this.post.hasOwnProperty('likedBy') && this.post.likedBy.length) {
        this.isLiked = true;
        this.countLikes = this.post.likedBy.length;
        console.log(this.post.likedBy);
        this.isLiked = this.findObjectByKey(this.post.likedBy,'username', this.userName);
        //console.log(this.countLikes);
      }
    });
  }
  like(event) {
    console.log(event.target);
    if(!this.post.hasOwnProperty('id')) {
      return false;
    }
    let componentFactory = this.componentFactoryResolver.resolveComponentFactory(SpinnerTagComponent);
    const spinnerComponent =  this.viewContainerRefLike.createComponent(componentFactory);
    this.spinnerTag = true;
    this.render.appendChild(event.target, spinnerComponent.location.nativeElement);
    this._blogService.postLike(this.post.id).subscribe(status =>{
      if(status.status) {
        this.spinnerTag = false;
        spinnerComponent.destroy();
        this.countLikes = status.message
        this.isLiked = true;
      }else {
        console.log(status.message);
      }
    });
    return false;
  }
  unLike(event) {
    if(!this.post.hasOwnProperty('id')) {
      return false;
    }
    let componentFactory = this.componentFactoryResolver.resolveComponentFactory(SpinnerTagComponent);
    const spinnerComponent =  this.viewContainerRefLike.createComponent(componentFactory);
    this.spinnerTag = true;
    this.render.appendChild(event.target, spinnerComponent.location.nativeElement);
    this._blogService.postUnLike(this.post.id).subscribe(status =>{
      this.spinnerTag = false;
      spinnerComponent.destroy();
      if(status.status) {
        this.countLikes = status.message;
        this.isLiked = false;
      }else {
        console.log(status.message);
      }
    });
    return false;
  }
  findObjectByKey(array, key, value) {
    for (let i = 0; i < array.length; i++) {
      if (array[i][key] === value) {
        //return array[i];
        return true;
      }
    }
    return false;
  }
}
