import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class TokenManagerService {

  private tokenKey: string = 'app_token';
  constructor() { }

  public setToken(content:Object) {
    localStorage.setItem(this.tokenKey, JSON.stringify(content));
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
  public getToken() {
    return this.getData('token');
  }
  public getUserName() {
    return this.getData('username');
  }
  public getRefreshToken() {
    return this.getData('refresh_token');
  }
  public removeToken() {
    return localStorage.removeItem(this.tokenKey);
  }
}
