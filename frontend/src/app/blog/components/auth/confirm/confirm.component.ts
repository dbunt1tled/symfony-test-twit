import { Component, OnInit } from '@angular/core';
import {AuthService} from '../../../../http/auth/auth.service';
import {Status} from '../../../models/common/status';
import {ActivatedRoute} from '@angular/router';

@Component({
  selector: 'app-confirm',
  templateUrl: './confirm.component.html',
  styleUrls: ['./confirm.component.sass']
})
export class ConfirmComponent implements OnInit {
  private token: string = '';
  private status: Status = {
    status: null,
    message: null,
  };
  constructor(
    private _authService: AuthService,
    private _activatedRoute: ActivatedRoute,
  ) { }

  ngOnInit() {
    this.token = this._activatedRoute.snapshot.params['token'];
    this._authService.confirm(this.token).then( (status: Status) =>{
      this.status = status;
    }).catch(error => {
      this.status = {
        status: false,
        message: 'Technical Problem Try Again'
      }
      console.log(error);
      return false;
    });
  }

}
