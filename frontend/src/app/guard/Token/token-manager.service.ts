import { Injectable } from '@angular/core';
import {RefreshToken} from '../../blog/models/auth/refresh-token';
import {Token} from '../../blog/models/auth/token';
//import {AuthService} from '../../http/auth/auth.service';

@Injectable({
  providedIn: 'root'
})
export class TokenManagerService {

  private tokenKey: string = 'app_token';
  constructor(
    //private _authService: AuthService,
  ) { }

  public setToken(content:Token) {
    localStorage.setItem(this.tokenKey, JSON.stringify(content));
    // this._authService.updateLoginData(content);
  }
  private getTokenFromStorage() {
    let storedToken:string = localStorage.getItem(this.tokenKey);
    if(!storedToken) throw 'no token found';
    return storedToken;
  }
  private getData(key) {
    // let currentTime:number = (new Date()).getTime();
    let data = null;
    try {
      let storedToken = JSON.parse(this.getTokenFromStorage());
      // if(storedToken.ttl < currentTime) throw 'invalid token found';
      if(storedToken.hasOwnProperty(key)) {
        data = storedToken[key];
      }
    }
    catch(err) {
      //console.error(err);
      return null;
    }
    return data;
  }
  public getFullToken(): Token {
    try {
      return JSON.parse(this.getTokenFromStorage());
    }
    catch(err) {
      return null;
    }
  }
  public getToken() {
    return this.getData('token');
  }
  public getUserName() {
    return this.getData('username');
  }
  public getRefreshToken() {
    let token: RefreshToken = {
      refresh_token: this.getData('refresh_token'),
    };
    return token;
  }
  public removeToken() {
    localStorage.removeItem(this.tokenKey);
    // this._authService.updateLoginData(null);
  }
}
