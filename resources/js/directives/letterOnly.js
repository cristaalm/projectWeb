export default {
  mounted(el) {
    el.addEventListener('input', e => {
      e.target.value = e.target.value.replace(/[^a-z\s]/gi, '')
    })
  },
}
