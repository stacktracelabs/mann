import {markRaw, ref, watchEffect} from "vue";
import type {ComputedRef, Ref} from "vue";
import type {IFilter, IFilterableComponent, IFilterableDef} from "./types";
import {router, usePage} from "@inertiajs/vue3";
import qs from "qs";
import {resolveFilterComponent} from "./main";

export function useFilter(filter: ComputedRef<IFilter> | Ref<IFilter>) {
  const components = ref<Array<IFilterableComponent>>([])

  const setValue = (filterable: IFilterableDef, value: any) => {
    const query = getQueryParams()

    // TODO: Add support query param name
    const queryParamName = filterable.filterable.id

    if (Array.isArray(value)) {
      if (value.length === 0) {
        delete query[queryParamName]
      } else {
        query[queryParamName] = value
      }
    } else if (value) {
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

    router.visit(url, {
      preserveState: true
    })
  }

  const shouldUpdateQuery = (newQuery: any) => {
    const currentQuery = JSON.stringify(getQueryParams()).split('').sort().join('')
    const updatedQuery = JSON.stringify(newQuery).split('').sort().join('')

    return currentQuery !== updatedQuery
  }

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
