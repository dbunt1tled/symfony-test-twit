/* "Barrel" of Http Interceptors */
import { HTTP_INTERCEPTORS } from '@angular/common/http';
import {AuthInterceptService} from './auth-intercept.service';
import {TrimInterceptService} from './trim-intercept.service';
import {JwtInterceptService} from './jwt-intercept.service';
import {SpinnerInterceptService} from './spinner-intercept.service';

export const httpInterceptorProviders = [
  { provide: HTTP_INTERCEPTORS, useClass: AuthInterceptService, multi: true },
  { provide: HTTP_INTERCEPTORS, useClass: TrimInterceptService, multi: true },
  { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptService, multi: true },
  { provide: HTTP_INTERCEPTORS, useClass: SpinnerInterceptService, multi: true },
];
