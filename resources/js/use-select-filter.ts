import {reactive, ref, watch} from "vue";
import type {ComputedRef} from "vue";
import type {ISelectFilterable} from "./types";

export function useSelectFilter(value: ComputedRef, filterable: ISelectFilterable, onUpdate: (value: any) => void) {
  // Select
  const selectedValue = ref(value.value)
  const onSelectionChange = () =>{
    onUpdate(selectedValue.value)
  }

  // Checkbox
  const createCheckboxMap = (value: any) => {
    const map: Record<string, boolean> = {}

    const selections = Array.isArray(value) ? value : []

    filterable.options.forEach(option => {
      map[option.id] = selections.includes(option.id)
    })

    return map
  }
  const checkedValues = reactive(createCheckboxMap(value.value))
  const onCheckedChange = () => {
    onUpdate(Object.keys(checkedValues).filter(it => checkedValues[it]))
  }

  watch(value, newValue => {
    // Select value
    selectedValue.value = newValue

    // Checkbox value
    const newMap = createCheckboxMap(value.value)
    Object.keys(newMap).forEach(key => {
      checkedValues[key] = newMap[key]
    })
  })

  return {
    selectedValue, onSelectionChange,
    checkedValues, onCheckedChange,
  }
}
