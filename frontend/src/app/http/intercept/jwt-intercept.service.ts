import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

import {AuthService} from '../auth/auth.service';
import {Router} from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class JwtInterceptService  implements HttpInterceptor{

  constructor(
    private _authService: AuthService,
    private _router: Router
  ) { }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return next.handle(request).pipe(catchError(err => {
      if (err.status === 401) {
        // auto logout if 401 response returned from api
        this._authService.logout().then( () => {
          console.log(this._router.url);
          return false;
          if(this._router.url !== 'login'){
            this._router.navigate(['login']);
          }
          return false;
        });

      }
      const error = err.error.message || err.statusText;
      return throwError(error);
    }))
  }
}
