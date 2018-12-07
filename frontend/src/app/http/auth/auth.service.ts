import { Injectable } from '@angular/core';
import {UserLogin} from '../../blog/models/auth/user-login';
import {BlogService} from '../../blog/services/blog.service';
import {TokenManagerService} from '../../guard/Token/token-manager.service';
import {BehaviorSubject, of} from 'rxjs';
import {Token} from '../../blog/models/auth/token';
import {UserRegister} from '../../blog/models/auth/user-register';
import {Status} from '../../blog/models/common/status';
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

  login(user: UserLogin) {
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

  confirm(token: string) {
    return new Promise( (resolve, reject) => {
      let resultStatus: Status = {'status':false, 'message':'Fail'};
      this._blogService.confirm(token).subscribe( status => {
        if(status.status) {
          resolve(status);
        } else {
          resolve(status);
        }/**/
      }, (error) => {
        reject(resultStatus);
      });
    });
  }

  register(user: UserRegister) {
    return new Promise( (resolve, reject) => {
      let resultStatus: Status = {'status':false, 'message':'Fail'};
      this._blogService.register(user).subscribe( status => {
        if(status.status) {
          resolve(status);
        } else {
          resolve(status);
        }/**/
      }, (error) => {
        reject(resultStatus);
      });
    });
  }
  logout() {
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
    window.location.href = '/login';
    /*if(this._router.url !== 'login'){
      return this._router.navigate(['login']);
    }/**/
    return false;
  }
  redirectToMain() {
    window.location.href = '/';
    /*if(this._router.url !== ''){
      return this._router.navigate(['/']);
    }/**/
    return false;
  }
}
