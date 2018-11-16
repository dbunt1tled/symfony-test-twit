import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LayoutComponent } from './containers/layout/layout.component';
import { HeaderComponent } from './components/header/header.component';
import { RouterModule } from '@angular/router';
import { FooterComponent } from './components/footer/footer.component';
import { CategoriesAllComponent } from '../blog/components/widgets/categories-all/categories-all.component';
import { TreeNodeComponent } from '../blog/components/widgets/blocks/tree-node/tree-node.component';
import {NotificationComponent} from '../blog/components/widgets/notification/notification.component';
import {FlashMessagesModule} from 'angular2-flash-messages';

@NgModule({
  declarations: [LayoutComponent, HeaderComponent, FooterComponent,CategoriesAllComponent, TreeNodeComponent,NotificationComponent],
  imports: [
    CommonModule,
    FlashMessagesModule.forRoot(),
    RouterModule
  ]
})
export class UiModule { }
