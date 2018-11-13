import { Injectable } from '@angular/core';
import {UserLogin} from '../../blog/models/auth/user-login';
import {BlogService} from '../../blog/services/blog.service';
import {TokenManagerService} from '../../guard/Token/token-manager.service';
import {BehaviorSubject, of} from 'rxjs';
import {RefreshToken} from '../../blog/models/auth/refresh-token';
import {Token} from '../../blog/models/auth/token';
import {Router} from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private loginData = new BehaviorSubject<Token>(this._tokenService.getFullToken());
  constructor(
    private _blogService: BlogService,
    private _tokenService: TokenManagerService,
    private _router: Router,
  ) { }

  login (user: UserLogin) {
    return new Promise( (resolve, reject) => {
      this._blogService.loginCheck(user).subscribe(token => {
        if(!!token) {
          this._tokenService.setToken(token);
          this.updateLoginData(token);
          resolve(true);
        } else {
          this.updateLoginData(null);
          reject(false);
        }/**/
      }, (error) => {
        this.updateLoginData(null);
        reject(false);
      });
    });
  }
  logout () {
    return new Promise( (resolve, reject) => {
      this._tokenService.removeToken();
      this.updateLoginData(null);
      resolve(true);
    });
  }
  updateLoginData(data) {
    this.loginData.next(data);
  }
  isLogin() {
    return this.loginData.asObservable();
  }
  refreshToken() {
    return new Promise( (resolve, reject) => {
      let token = this._tokenService.getRefreshToken();
      this._blogService.refreshToken(token).subscribe(newToken => {
        if(!!newToken){
          this._tokenService.setToken(newToken);
          this.updateLoginData(newToken);
          resolve(true);
        }
        reject(false);
      }, error => {
        reject(error);
      });
    });
  }
  redirectToLogin() {
    if(this._router.url !== 'login'){
      return this._router.navigate(['login']);
    }
    return false;
  }
  redirectToMain() {
    if(this._router.url !== ''){
      return this._router.navigate(['/']);
    }
    return false;
  }
}
