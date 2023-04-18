import type {Component} from "vue";

export type FSetValue = (value: any) => void

export interface ISelectOption {
  id: string
  title: string
  extra: Record<string, any>
}

export interface ISelectFilterable {
  id: string
  title: string
  component: 'select'
  emptyValue: any
  queryParameter: string
  options: Array<ISelectOption>
  emptyOptionTitle: string
  multiple: boolean
}

export interface IRangeFilterable {
  id: string
  title: string
  component: 'range'
  emptyValue: any
  queryParameter: string
  min: number
  max: number
  step: number
  minAttribute: string
  maxAttribute: string
}

export type IFilterable = ISelectFilterable | IRangeFilterable

export interface IFilterableDef<T = any> {
  filterable: IFilterable
  value: T
}

export interface IFilter {
  filterables: Array<IFilterableDef>
}

export interface IFilterableComponent {
  filterable: IFilterable
  component: Component | any
  value: any
  setValue: FSetValue
}
