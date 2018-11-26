import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BlogRoutingModule } from './blog-routing.module';
import { PostsComponent } from './components/posts/posts.component';
import { PostComponent } from './components/post/post.component';
import { DateFromSecPipe } from './pipes/date-from-sec.pipe';
import { LoginComponent } from './components/auth/login/login.component';
import { RegisterComponent } from './components/auth/register/register.component';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import { ConfirmComponent } from './components/auth/confirm/confirm.component';
import { NotificationUnreadComponent } from './components/notification-unread/notification-unread.component';
import { SpinnerTagComponent } from './components/spinner-tag/spinner-tag.component';
import { UserComponent } from './components/user/user.component';

@NgModule({
  declarations: [PostsComponent, PostComponent, DateFromSecPipe, LoginComponent, RegisterComponent, ConfirmComponent, NotificationUnreadComponent, SpinnerTagComponent, UserComponent],
  entryComponents: [SpinnerTagComponent],
  imports: [
    CommonModule,
    BlogRoutingModule,
    FormsModule,
    ReactiveFormsModule,
  ]
})
export class BlogModule { }
