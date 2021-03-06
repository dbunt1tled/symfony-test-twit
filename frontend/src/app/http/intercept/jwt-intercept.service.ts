import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import {Observable, of, throwError} from 'rxjs';
import {catchError, flatMap} from 'rxjs/operators';
import { AuthService } from '../auth/auth.service';

@Injectable({
  providedIn: 'root'
})
export class JwtInterceptService  implements HttpInterceptor{

  constructor(
    private _authService: AuthService,
  ) { }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return next.handle(request).pipe(catchError(err => {
      if (err.status === 401) {
        // auto logout if 401 response returned from api
        this._authService.isLogin()
          .pipe(
            flatMap( token =>{
              if(!!token){
                return this._authService.refreshToken();
              }
              return of(false)
            })
          ).subscribe( (status:any) => {
          if(!!status) {
            return this._authService.redirectToMain();
          }
          return this._authService.redirectToLogin();
        });
      }
      const error = err.error.message || err.statusText;
      return throwError(error);
    }))
  }
}
