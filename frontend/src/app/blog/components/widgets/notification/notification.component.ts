import { Component, OnInit } from '@angular/core';
import { BlogService } from '../../../services/blog.service';
import {AuthService} from '../../../../http/auth/auth.service';
import {flatMap} from 'rxjs/operators';
import {of} from 'rxjs';

@Component({
  selector: 'app-notification',
  templateUrl: './notification.component.html',
  styleUrls: ['./notification.component.sass']
})
export class NotificationComponent implements OnInit {
  count: number = null;
  userName: string = null;
  constructor(
    private _blogService: BlogService,
    private _authService: AuthService,
  ) { }

  ngOnInit() {
    this._authService.isLogin()
      .pipe(
        flatMap( token =>{
          if(!!token){
            this.userName = token.username;
            return this._blogService.getNotificationUnreadCount();
          }
          return of(false)
        })
      ).subscribe( (count:any) => {
        if(!count) {
          return false;
        }
        this.count = count.count;
    });
  }

}
