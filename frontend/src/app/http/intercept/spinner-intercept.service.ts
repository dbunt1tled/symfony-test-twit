import { Injectable } from '@angular/core';
import {HttpEvent, HttpHandler, HttpInterceptor, HttpRequest, HttpResponse} from '@angular/common/http';
import {Observable} from 'rxjs';
import {SpinnerService} from '../../ui/service/spinner.service';
import {tap} from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class SpinnerInterceptService implements HttpInterceptor{

  constructor(
    private _spinnerService: SpinnerService,
  ) { }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    this._spinnerService.onStarted(request);
    return next.handle(request).pipe(
      tap( event => {
        if (event instanceof HttpResponse) {
          this._spinnerService.onFinished(request);
        }
      })
    );
  }
}
