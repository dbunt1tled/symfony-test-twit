export interface NodeInterface {
  _id: any,
  title: string,
  slug: string,
  description:any,
  level: any,
  enabled: boolean,
  __children: Array<Node>,
}
