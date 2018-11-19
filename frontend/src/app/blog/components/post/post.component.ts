import {Component, ComponentFactoryResolver, OnInit, ViewChild, ViewContainerRef} from '@angular/core';
import {BlogService} from '../../services/blog.service';
import {ActivatedRoute} from '@angular/router';
import {SpinnerTagComponent} from '../spinner-tag/spinner-tag.component';

@Component({
  selector: 'app-post',
  templateUrl: './post.component.html',
  styleUrls: ['./post.component.sass']
})
export class PostComponent implements OnInit {
  post: any;
  isLiked: boolean = false;
  countLikes: any = 0;
  @ViewChild('spinnertag', {read: ViewContainerRef}) viewContainerRef: ViewContainerRef;

  constructor(
    private componentFactoryResolver: ComponentFactoryResolver,
    private _blogService: BlogService,
    private _activatedRoute: ActivatedRoute,
  ) { }

  ngOnInit() {
    let slug = this._activatedRoute.snapshot.params['slug'];
    this._blogService.getPost(slug).subscribe(post =>{
      this.post = post;
      console.log(post);
      let componentFactory = this.componentFactoryResolver.resolveComponentFactory(SpinnerTagComponent);
      this.viewContainerRef.createComponent(componentFactory);

      if(this.post.hasOwnProperty('likedBy') && this.post.likedBy.hasOwnProperty('0')) {


        this.isLiked = true;
        this.countLikes = Object.keys(this.post.likedBy).reduce((a, b) => { return this.post.likedBy[a] > this.post.likedBy[b] ? a : b });
        console.log(this.countLikes);
      }
    });
  }
  like(event) {

    return false;
  }
  unLike(event) {

  }
}
