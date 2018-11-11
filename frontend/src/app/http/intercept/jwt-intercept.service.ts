import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

import {AuthService} from '../auth/auth.service';
import {Router} from '@angular/router';
import {TokenManagerService} from '../../guard/Token/token-manager.service';

@Injectable({
  providedIn: 'root'
})
export class JwtInterceptService  implements HttpInterceptor{

  constructor(
    private _authService: AuthService,
    private _tokenService: TokenManagerService,
    private _router: Router
  ) { }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return next.handle(request).pipe(catchError(err => {
      if (err.status === 401) {
        // auto logout if 401 response returned from api
        let token = this._tokenService.getRefreshToken();
        this._authService.refreshToken(token).subscribe(newToken => {
          this._tokenService.setToken(newToken);
          this._router.navigate(['/']);
          return false;
        }, error=>{
          this._authService.logout().then( () => {
            if(this._router.url !== 'login'){
              this._router.navigate(['login']);
            }
            return false;
          });

        });
        /*
        /**/

      }
      const error = err.error.message || err.statusText;
      return throwError(error);
    }))
  }
}
