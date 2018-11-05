import { Component, Input, forwardRef, OnInit } from '@angular/core';
import {NodeInterface} from './node';

@Component({
  selector: 'app-tree-node',
  templateUrl: './tree-node.component.html',
  styleUrls: ['./tree-node.component.sass']
})
export class TreeNodeComponent implements OnInit {
  @Input() public node: Array<NodeInterface>;

  constructor() { }

  ngOnInit() {
  }
  onClick(event)
  {
    let nodeLi = event.target.parentNode.parentNode;
    nodeLi.classList.toggle('active');
    let nodeUl = nodeLi.querySelector('ul');
    if (nodeLi.classList.contains('active')) {
      nodeUl.style.display = 'block';
    } else {
      nodeUl.style.display = 'none';
    }
  }
}
