// PLUGIN PATHS
var path = myThemeParams.plugiPath;
const { createApp } = Vue;

jQuery(document).ready(function ($) {
  $(".ajax-link").click(function () {});
});

createApp({
  data() {
    return {
      isDisabled: true,
      checked: false,
      error: "",
      message: "",
      name: "",
      last_name: "",
      email: "",
    };
  },
  methods: {
    getOptions() {},
    onChange(event) {
      var optionText = event.target.value;
      console.log(optionText, this.checked);
      if (this.checked && this.email !== "") {
        this.isDisabled = false;
      } else {
        this.isDisabled = true;
      }
    },
    sendForm() {
      this.isDisabled = true;
      this.error = "";
      this.message = "";
      var self = this;

      jQuery.ajax({
        type: "POST",
        url: "/wp-admin/admin-ajax.php",
        data: {
          action: "mark_message_as_read",
          // add your parameters here
          email: self.email, //"john.doe@example.com",
          password: self.email,
          firstname: self.name,
          lastname: self.last_name,
        },
        success: function (output) {
          console.log(output.data);
          if (output.data && output.data.error) {
            const msg = output.data.error.split(".");
            console.log(msg);
            // self.error = msg[0];
            self.error =
              " Esa dirección de correo electrónico ya está registrada. Prueba con otra por favor.";
          } else {
            self.message =
              "¡Listo!. Esperamos te sean muy útiles los primeros capítulos del libro.";
          }

          self.isDisabled = false;
          self.email = "";
          self.name = "";
          self.last_name = "";
        },
      });
    },
  },
  mounted() {
    this.getOptions();
  },
}).mount("#app");
