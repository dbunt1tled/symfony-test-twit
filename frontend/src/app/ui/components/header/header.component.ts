import { Component, OnInit } from '@angular/core';
import {AuthService} from '../../../http/auth/auth.service';
import {Router} from '@angular/router';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.sass']
})
export class HeaderComponent implements OnInit {
  public logo = 'asset/logo.svg';
  public title = 'Test 1';
  public isLogin: any = false;
  public userName: string;
  constructor(
    private _authService: AuthService,
    private _router: Router
  ) { }

  ngOnInit() {
    this._authService.isLogin().then( (status) => {
     this.isLogin = status;
    });
  }

  logout() {
    this._authService.logout().then( () =>{
      this._router.navigate(['login']);
    });
  }
}
