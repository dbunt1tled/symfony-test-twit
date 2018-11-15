import {NgModule} from '@angular/core';
import {Routes, RouterModule} from '@angular/router';
import {PostsComponent} from './components/posts/posts.component';
import {PostComponent} from './components/post/post.component';
import {LoginComponent} from './components/auth/login/login.component';
import {RegisterComponent} from './components/auth/register/register.component';
import {ConfirmComponent} from './components/auth/confirm/confirm.component';

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
    path: 'register',
    component: RegisterComponent,
  },
  {
    path: 'confirm/:token',
    component: ConfirmComponent,
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
