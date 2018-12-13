import {NgModule} from '@angular/core';
import {Routes, RouterModule} from '@angular/router';
import {PostsComponent} from './components/posts/posts.component';
import {PostComponent} from './components/post/post.component';
import {LoginComponent} from './components/auth/login/login.component';
import {RegisterComponent} from './components/auth/register/register.component';
import {ConfirmComponent} from './components/auth/confirm/confirm.component';
import {NotificationUnreadComponent} from './components/notification-unread/notification-unread.component';
import {SpinnerTagComponent} from './components/spinner-tag/spinner-tag.component';
import {UserComponent} from './components/user/user.component';
import {PostManageComponent} from './components/post-manage/post-manage.component';

// определение дочерних маршрутов
const postRoutes: Routes = [
  { path: 'manage', component: PostManageComponent},
];
const routes: Routes = [
  { path: '', component: PostsComponent, /* resolve: {} /**/ },
  { path: 'login', component: LoginComponent, },
  { path: 'register', component: RegisterComponent, },
  { path: 'confirm/:token', component: ConfirmComponent, },
  { path: 'user/:username', component: UserComponent, },
  { path: 'notification/all', component: NotificationUnreadComponent, },
  { path: 'manage/add', component: PostManageComponent },
  { path: ':slug/manage', component: PostManageComponent },
  { path: ':slug', component: PostComponent, },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class BlogRoutingModule {
}
