import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BlogRoutingModule } from './blog-routing.module';
import { PostsComponent } from './components/posts/posts.component';
import { PostComponent } from './components/post/post.component';
import { TreeNodeComponent } from './components/widgets/blocks/tree-node/tree-node.component';
import {FooterComponent} from '../ui/components/footer/footer.component';
import {CategoriesAllComponent} from './components/widgets/categories-all/categories-all.component';
import { DateFromSecPipe } from './pipes/date-from-sec.pipe';

@NgModule({
  declarations: [PostsComponent, PostComponent, DateFromSecPipe],
  imports: [
    CommonModule,
    BlogRoutingModule
  ]
})
export class BlogModule { }
