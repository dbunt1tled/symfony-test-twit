import { Component, OnInit } from '@angular/core';
import {BlogService} from '../../services/blog.service';

@Component({
  selector: 'app-posts',
  templateUrl: './posts.component.html',
  styleUrls: ['./posts.component.sass']
})
export class PostsComponent implements OnInit {

  private posts: any;
  private loaded: boolean = false;
  constructor(
    private _blogService: BlogService,
  ) { }

  ngOnInit() {
    this.getPosts();
  }

  getPosts() {
    this._blogService.getPosts(1,20).subscribe(posts => {
      this.posts = posts;
      this.loaded = true;
    });
  }
  setPosts(postsData) {
    console.log(postsData);
    if(!postsData) {
      this._blogService.getPosts(1,20).subscribe(posts => {
        this.posts = posts;
        this.loaded = true;
      });
    }else{
      this.posts = postsData;
      this.loaded = true;
    }
    console.log(this.posts);
  }
}
