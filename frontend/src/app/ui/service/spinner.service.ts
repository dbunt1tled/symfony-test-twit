import { Injectable } from '@angular/core';
import {BehaviorSubject, Observable} from 'rxjs';
import {HttpRequest} from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class SpinnerService {

  private _loading = new BehaviorSubject<boolean>(false);
  private requests: HttpRequest<any>[] = [];
  constructor() { }

  onStarted(req: HttpRequest<any>): void {
    this.requests.push(req);
    this.notify();
  }

  onFinished(req: HttpRequest<any>): void {
    const index = this.requests.indexOf(req);
    if (index !== -1) {
      this.requests.splice(index, 1);
    }
    this.notify();
  }
  getSpinnerStatus(): Observable<boolean> {
    return this._loading.asObservable();
  }
  private notify(): void {
    this._loading.next(this.requests.length !== 0);
  }
}
