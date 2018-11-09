import {NgModule} from '@angular/core';
import {Routes, RouterModule} from '@angular/router';
import {PostsComponent} from './components/posts/posts.component';
import {PostComponent} from './components/post/post.component';
import {LoginComponent} from './components/auth/login/login.component';

const routes: Routes = [
  {
    path: '',
    component: PostsComponent,
    // resolve: {}
  },
  {
    path: 'login',
    component: LoginComponent,
  },
  {
    path: ':slug',
    component: PostComponent,
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BlogRoutingModule {
}
