import Button from './Button.vue'
import Panel from './Panel.vue'
import Popover from './Popover.vue'

const DialogComponent = Object.assign({}, Popover, {
  Button: Button,
  Panel: Panel,
})

export default DialogComponent
