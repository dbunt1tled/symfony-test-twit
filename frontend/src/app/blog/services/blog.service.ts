import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class BlogService {
  apiClient: string = environment.blogApi;
  token: string = '';
  constructor(
    private _http: HttpClient
  ) { }

  /**
   * @param page
   * @param limit
   */
  getPosts(page: number, limit: number) {
    return this._http.get(`${this.apiClient}/posts?page=${page}&limit=${limit}&apiKey=${this.token}`);
  }
  getCategoriesTreeAll() {
    return this._http.get(`${this.apiClient}/category/tree-all?apiKey=${this.token}`);
  }
}