import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import {UserLogin} from '../models/auth/user-login';
import {RefreshToken} from '../models/auth/refresh-token';
import {Token} from '../models/auth/token';

@Injectable({
  providedIn: 'root'
})
export class BlogService {
  apiClient: string = environment.blogApi;
  constructor(
    private _http: HttpClient
  ) { }

  /**
   * @param page
   * @param limit
   */
  getPosts(page: number, limit: number) {
    return this._http.get(`${this.apiClient}/posts?page=${page}&limit=${limit}`);
  }
  getCategoriesTreeAll() {
    return this._http.get(`${this.apiClient}/category/tree-all`);
  }
  loginCheck(user: UserLogin) {
    return this._http.post<Token>(`${this.apiClient}/login_check`,user);
  }
  refreshToken(refreshToken: RefreshToken) {
    return this._http.post<Token>(`${this.apiClient}/token/refresh`,refreshToken);
  }
}
