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
  public getToken() {
    // let currentTime:number = (new Date()).getTime();
    let token = null;
    try {
      let storedToken = JSON.parse(this.getTokenFromStorage());
      // if(storedToken.ttl < currentTime) throw 'invalid token found';
      token = storedToken.token;
    }
    catch(err) {
      console.error(err);
      return false;
    }
    return token;
  }
}
