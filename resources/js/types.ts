import type {Component} from "vue";

export type FSetValue = (value: any) => void

export interface ISelectOption {
  id: string
  title: string
}

export interface ISelectFilterable {
  id: string
  title: string
  component: 'select'
  options: Array<ISelectOption>
  emptyTitle: string
  emptyValue: any
  multiple: boolean
}

export type IFilterable = ISelectFilterable

export interface IFilterableDef<T = any> {
  filterable: IFilterable
  value: T
}

export interface IFilter {
  filterables: Array<IFilterableDef>
}

export interface IFilterableComponent {
  filterable: IFilterable
  component: Component
  value: any
  setValue: FSetValue
}
