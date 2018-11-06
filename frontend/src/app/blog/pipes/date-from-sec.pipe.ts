import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'dateFromSec'
})
export class DateFromSecPipe implements PipeTransform {

  transform(value: any, locale?: any, format?: any, args?: any): any {
    const date = new Date(value.sec * 1000);
    let result;
    switch (format) {
      case 'full':
        result = date.toLocaleDateString(locale, {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});
        break;
      case 'short':
        result = date.toLocaleDateString(locale, {weekday: 'short', year: 'numeric', month: 'short', day: 'numeric'});
        break;
      case 'default':
        result = date.toLocaleDateString(locale);
        break;
    }
    return result;
  }

}
