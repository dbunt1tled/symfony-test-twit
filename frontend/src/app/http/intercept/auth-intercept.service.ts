import { Injectable } from '@angular/core';
import {HttpEvent, HttpHandler, HttpInterceptor, HttpRequest} from '@angular/common/http';
import {Observable, from} from 'rxjs';
import {AuthService} from '../auth/auth.service';
import { switchMap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class AuthInterceptService implements HttpInterceptor{

  constructor(
    private _authService: AuthService,
  ) { }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return from(this._authService.isLogin()).pipe(
      switchMap(token => {
        if(!!token) {
          let tokenNew = request.clone({
            setHeaders: {
              'Authorization': `Bearer ${token.token}`,
            }
          });
          return next.handle(tokenNew);
        }else{
          return next.handle(request);
        }
      }));
  }
}
