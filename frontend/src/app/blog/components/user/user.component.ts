import {Component, ComponentFactoryResolver, OnInit, Renderer2, ViewChild, ViewContainerRef} from '@angular/core';
import {BlogService} from '../../services/blog.service';
import {ActivatedRoute} from '@angular/router';
import {AuthService} from '../../../http/auth/auth.service';
import {flatMap} from 'rxjs/operators';
import {PostsComponent} from '../posts/posts.component';
import {SpinnerTagComponent} from '../spinner-tag/spinner-tag.component';

@Component({
  selector: 'app-user',
  templateUrl: './user.component.html',
  styleUrls: ['./user.component.sass']
})
export class UserComponent implements OnInit {
  @ViewChild('postTemplate', {read: ViewContainerRef}) viewContainerRefPost: ViewContainerRef;
  @ViewChild('spinnerTagWrap', {read: ViewContainerRef}) viewContainerRefFollow: ViewContainerRef;

  spinnerTag: boolean = false;
  userName: string = '';
  posts: any = null;
  user: any = null;
  isFollow: boolean = false;
  constructor(
    private componentFactoryResolver: ComponentFactoryResolver,
    private _blogService: BlogService,
    private _activatedRoute: ActivatedRoute,
    private render: Renderer2,
    private _authService: AuthService,
  ) { }

  ngOnInit() {

    let userNameView = this._activatedRoute.snapshot.params['username'];
    this._authService.isLogin()
      .pipe(
        flatMap( token =>{
          if(!!token){
            this.userName = token.username;
          }
          return this._blogService.getUserWithPosts(userNameView);
        })
      ).subscribe( data =>{
        if(data.hasOwnProperty('posts') && data.posts.length){
          this.posts = data.posts;
        }
        if(data.hasOwnProperty('user') && data.user){
          this.user = data.user;
        }
        if(this.userName) {
          this.isFollow = this.findObjectByKey(this.user.followers,'username', this.userName);
        }
        if(this.posts) {
          /*
          let componentFactory = this.componentFactoryResolver.resolveComponentFactory(PostsComponent);
          const postComponent =  this.viewContainerRefPost.createComponent(componentFactory);
          postComponent.instance.setPosts(this.posts);
          //this.rootViewContainer.insert(component.hostView)
          //this.render.appendChild(event.target, postComponent.location.nativeElement);/**/
        }
    });
  }
  follow(event) {

    if(!this.user.hasOwnProperty('id')) {
      return false;
    }
    let componentFactory = this.componentFactoryResolver.resolveComponentFactory(SpinnerTagComponent);
    const spinnerComponent =  this.viewContainerRefFollow.createComponent(componentFactory);
    this.spinnerTag = true;
    this.render.appendChild(event.target, spinnerComponent.location.nativeElement);
    this._blogService.followUser(this.user.id).subscribe(status => {
      if(status.status) {
        this.spinnerTag = false;
        spinnerComponent.destroy();
        this.isFollow = true;
      }else {
        console.warn(status.message);
        spinnerComponent.destroy();
      }
    });
    return false;
  }
  unFollow(event) {

    if(!this.user.hasOwnProperty('id')) {
      return false;
    }
    let componentFactory = this.componentFactoryResolver.resolveComponentFactory(SpinnerTagComponent);
    const spinnerComponent =  this.viewContainerRefFollow.createComponent(componentFactory);
    this.spinnerTag = true;
    this.render.appendChild(event.target, spinnerComponent.location.nativeElement);
    this._blogService.unFollowUser(this.user.id).subscribe(status => {
      this.spinnerTag = false;
      spinnerComponent.destroy();
      if(status.status) {
        this.isFollow = false;
      }else {
        console.warn(status.message);
        spinnerComponent.destroy();
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
