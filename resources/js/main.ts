import type {Component} from 'vue'

const ComponentRegistry: Record<string, () => Component> = {}

export function registerFilterComponent(name: string, factory: () => Component) {
  ComponentRegistry[name] = factory
}

export function resolveFilterComponent(name: string): Component {
  return ComponentRegistry[name]()
}

export * from './types'
export * from './use-filter'
export * from './use-select-filter'
