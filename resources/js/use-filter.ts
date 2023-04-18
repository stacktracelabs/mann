import {markRaw, ref, watchEffect} from "vue";
import type {ComputedRef, Ref} from "vue";
import type {IFilter, IFilterableComponent, IFilterableDef} from "./types";
import {router, usePage} from "@inertiajs/vue3";
import type {VisitOptions} from "@inertiajs/core";
import qs from "qs";
import {resolveFilterComponent} from "./main";

function valuesMatches(a: any, b: any): boolean {
  if (a === b) {
    return true
  }

  if (a === null && b === null) {
    return true
  }

  if (Array.isArray(a) && Array.isArray(b) && a.length === 0 && b.length === 0) {
    return true
  }

  return JSON.stringify(a).split('').sort().join('') === JSON.stringify(b).split('').sort().join('')
}

export function useFilter(
  filter: ComputedRef<IFilter> | Ref<IFilter>,
  options: VisitOptions = {}
) {
  const components = ref<Array<IFilterableComponent>>([])

  const setValue = (filterableDef: IFilterableDef, value: any) => {
    const query = getQueryParams()

    const filterable = filterableDef.filterable

    const queryParamName = filterable.queryParameter

    if (! valuesMatches(value, filterable.emptyValue)) {
      query[queryParamName] = value
    } else {
      delete query[queryParamName]
    }

    if (shouldUpdateQuery(query)) {
      replaceQuery(query)
    }
  }

  const replaceQuery = (query: any) => {
    let url = usePage().url.split('?')[0]

    if (Object.keys(query).length > 0) {
      url += `?${qs.stringify(query)}`
    }

    router.visit(url, options)
  }

  const shouldUpdateQuery = (newQuery: any) => ! valuesMatches(getQueryParams(), newQuery)

  const getQueryParams = () => {
    return qs.parse(window.location.search.replace('?', ''))
  }

  watchEffect(() => {
    components.value = filter.value.filterables.map(filterableDef => {
      const filterable = filterableDef.filterable

      return {
        component: markRaw(resolveFilterComponent(filterable.component)),
        filterable,
        value: filterableDef.value,
        setValue: (value: any) => setValue(filterableDef, value)
      }
    })
  })

  return { components }
}
