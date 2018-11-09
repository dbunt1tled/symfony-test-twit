import { Injectable } from '@angular/core';
import {HttpEvent, HttpHandler, HttpInterceptor, HttpRequest} from '@angular/common/http';
import {Observable} from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class TrimInterceptService implements HttpInterceptor{

  constructor() { }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    // For Example on Login trim fields
    const body = request.body;
    if(!body || !body.hasOwnProperty('username') ) {
      return next.handle(request);
    }
    const newBody = {...body, username: body.username.trim()}
    const newReq = request.clone({ body: newBody });
    return next.handle(newReq);
  }
}
