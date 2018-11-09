import { Injectable } from '@angular/core';
import {HttpEvent, HttpHandler, HttpInterceptor, HttpRequest} from '@angular/common/http';
import {Observable} from 'rxjs';
import {TokenManagerService} from '../../guard/Token/token-manager.service';

@Injectable({
  providedIn: 'root'
})
export class AuthInterceptService implements HttpInterceptor{

  constructor(
    private _tokenManager: TokenManagerService,
  ) { }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    let tokenKey = this._tokenManager.getToken();
    if (!tokenKey) {
      return next.handle(request);
    }
    let token = request.clone({
      setHeaders: {
        'Authorization': `Bearer ${tokenKey}`,
      }
    });
    return next.handle(token);
  }
}
