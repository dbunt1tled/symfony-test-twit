import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import {UserLogin} from '../models/auth/user-login';
import {RefreshToken} from '../models/auth/refresh-token';
import {Token} from '../models/auth/token';
import {UserRegister} from '../models/auth/user-register';
import {Status} from '../models/common/status';
import {UnreadNotifications} from '../models/auth/unread-notifications';
import {UserPosts} from '../models/blog/user-posts';
import {Post} from '../models/blog/post';

@Injectable({
  providedIn: 'root'
})
export class BlogService {
  apiClient: string = environment.blogApi;
  constructor(
    private _http: HttpClient
  ) { }
  getPosts(page: number, limit: number) {
    return this._http.get(`${this.apiClient}/posts?page=${page}&limit=${limit}`);
  }
  getPost(slug: string) {
    return this._http.get<Post>(`${this.apiClient}/posts/${slug}`);
  }
  getPostForManage(slug: string) {
    return this._http.get<any>(`${this.apiClient}/posts/${slug}/manage`);
  }
  getCategoriesDropDown() {
    return this._http.get<any>(`${this.apiClient}/category/tree-all-drop-down`);
  }
  postUpdate(slug: string, post: Post) {
    return this._http.post<Status>(`${this.apiClient}/posts/${slug}/update`, post);
  }
  postAdd(post: Post) {
    return this._http.post<Status>(`${this.apiClient}/posts/add`, post);
  }
  getCategoriesTreeAll() {
    return this._http.get(`${this.apiClient}/category/tree-all`);
  }
  loginCheck(user: UserLogin) {
    return this._http.post<Token>(`${this.apiClient}/login_check`, user);
  }
  register(user: UserRegister) {
    return this._http.post<Status>(`${this.apiClient}/auth/register`, user);
  }
  refreshToken(refreshToken: RefreshToken) {
    return this._http.post<Token>(`${this.apiClient}/token/refresh`, refreshToken);
  }
  confirm(token: string) {
    return this._http.get<Status>(`${this.apiClient}/auth/confirm/${token}`);
  }
  getNotificationUnreadCount() {
    return this._http.get(`${this.apiClient}/notification/unread-count`);
  }
  getNotificationUnreadAll() {
    return this._http.get<UnreadNotifications>(`${this.apiClient}/notification/all`);
  }
  notificationMarkAsReadAll() {
    return this._http.post(`${this.apiClient}/notification/acknowledge-all`, {});
  }
  notificationMarkAsRead(id) {
    return this._http.post(`${this.apiClient}/notification/acknowledge`, {id: id});
  }
  postLike(id) {
    return this._http.post<Status>(`${this.apiClient}/likes/like`,{id:id});
  }
  postUnLike(id) {
    return this._http.post<Status>(`${this.apiClient}/likes/unlike`,{id:id});
  }
  getUserWithPosts(username: string) {
    return this._http.get<UserPosts>(`${this.apiClient}/posts/user/${username}`);
  }
  followUser(id) {
    return this._http.post<Status>(`${this.apiClient}/following/follow`,{id:id});
  }
  unFollowUser(id) {
    return this._http.post<Status>(`${this.apiClient}/following/unfollow`,{id:id});
  }
}
