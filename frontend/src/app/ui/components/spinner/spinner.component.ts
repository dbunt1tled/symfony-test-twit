import {Component, OnInit} from '@angular/core';
import {SpinnerService} from '../../service/spinner.service';

@Component({
  selector: 'app-spinner',
  templateUrl: './spinner.component.html',
  styleUrls: ['./spinner.component.sass']
})
export class SpinnerComponent implements OnInit {
  spinner: boolean = true;
  // percentage: number = 1;
  // timer: any;

  constructor(
    private _spinnerService: SpinnerService
  ) {
    // this.timer = this.frame();
  }

  ngOnInit() {
    this._spinnerService.getSpinnerStatus().subscribe(status => {
      this.spinner = status;
      /*if (!this.spinner) {
        clearInterval(this.timer);
      }/**/
    });
  }
  /*
  frame() {
    return setInterval(() => {
      if (this.percentage >= 100) {
        this.percentage = 0;
      } else {
        this.percentage = (this.percentage + 10);
      }
    }, 1000)
  }/**/

}
