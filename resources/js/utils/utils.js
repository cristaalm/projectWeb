export function deepEqual(obj1, obj2) {
  return JSON.stringify(obj1) === JSON.stringify(obj2)
}


export function toTitleCase(text) {
  return text
    .toLowerCase()
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

export function arraysHaveSameValues(arr1, arr2) {
  if (arr1.length !== arr2.length) return false

  const sorted1 = [...arr1].sort()
  const sorted2 = [...arr2].sort()

  return sorted1.every((value, index) => value === sorted2[index])
}
