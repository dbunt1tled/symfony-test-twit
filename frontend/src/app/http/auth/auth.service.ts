import { Injectable } from '@angular/core';
import {UserLogin} from '../../blog/models/auth/user-login';
import {BlogService} from '../../blog/services/blog.service';
import {TokenManagerService} from '../../guard/Token/token-manager.service';
import {of} from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  constructor(
    private _blogService: BlogService,
    private _tokenService: TokenManagerService,

  ) { }

  login (user: UserLogin) {
    return new Promise( (resolve, reject) => {
      this._blogService.loginCheck(user).subscribe(token => {
        if(token) {
          this._tokenService.setToken(token);
          resolve(true);
        } else {
          reject(false);
        }
      }, (error) => {
        reject(false);
      });
    });
  }
  logout () {
    return new Promise( (resolve, reject) => {
      this._tokenService.removeToken();
      resolve(true);
    });
  }
  isLogin() {
    return new Promise( (resolve, reject) => {
      resolve(!!this._tokenService.getToken());
    });
  }
  getUserName() {
    return of(this._tokenService.getUserName());
  }
}
