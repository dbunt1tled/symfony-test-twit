import { Component, OnInit } from '@angular/core';
import {BlogService} from '../../services/blog.service';
import {flatMap} from 'rxjs/operators';
import {StatusRegister} from '../../models/auth/status-register';

@Component({
  selector: 'app-notification-unread',
  templateUrl: './notification-unread.component.html',
  styleUrls: ['./notification-unread.component.sass']
})
export class NotificationUnreadComponent implements OnInit {
  notifications: any = null;
  constructor(
    private _blogService: BlogService,
  ) { }

  ngOnInit() {
    this._blogService.getNotificationUnreadAll().subscribe( notes => {
      return this.notifications = notes.notifications;
    });
  }

  markAll() {
    this._blogService.notificationMarkAsReadAll()
      .pipe(
        flatMap((status: StatusRegister) => {
          if (!status.status) {
            console.log(status.message);
          }
          return this._blogService.getNotificationUnreadAll();
        })
      )
      .subscribe(notes => {
        return this.notifications = notes.notifications;
      });
  }
  markSeen(event,id) {
    event.target.classList.toggle("active");
    let spinner = document.createElement('i');
    spinner.classList.add('fa', 'fa-circle', 'fa-circle-o-notch', 'fa-spin');
    event.target.appendChild(spinner);
    this._blogService.notificationMarkAsRead(id)
      .pipe(
        flatMap((status: StatusRegister) => {
          if (!status.status) {
            console.log(status.message);
          }
          return this._blogService.getNotificationUnreadAll();
        })
      )
      .subscribe(notes => {
        this.notifications = notes.notifications;
        event.target.classList.toggle("active");
        spinner.remove();
      });
  }
}
