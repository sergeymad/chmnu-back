<template>
  <div>
    <heading class="mb-6">Supportchat</heading>

    <card>
      <div class="relative overflow-hidden overflow-x-auto p-view">

        <div v-for="message in JSON.parse(messages[0].history)  ">
          <div class="toasted default m-2 w-1/2" v-if="message.direction === 'from'">
            {{ message.message }}
          </div>
          <div class="toasted success m-2 w-1/2 forward" v-else>
            {{ message.message }}
          </div>


        </div>
        <div class="relative h-9 flex-no-shrink mb-12" ><input v-model="response" v-on:keyup.enter="sendMessage" data-testid="message-input" placeholder="Enter your message"  spellcheck="false" class="appearance-none form-search w-full pl-search shadow" style="border-radius: 5px"></div>

      </div>
    </card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      messages: [],
      response: "",
      interval: ""
    }
  },
  mounted() {
    this.getMessages();

  },
  methods: {
    sendMessage(){
      Nova.request().get('/sendMessage/'+this.$route.params.id+'?message='+this.response)
      this.response="";
    },
    getMessages() {
      var instance = this
      Nova.request().get('/getMessages/'+instance.$route.params.id).then(response => {
        instance.messages = response.data;
      });
      this.interval = setInterval(function (){
        Nova.request().get('/getMessages/'+instance.$route.params.id).then(response => {
          instance.messages = response.data;
        });

      },2500)

    },

  },
  destroyed(){
    clearInterval(this.interval);
  }
}

</script>

<style>
.forward{
  margin-left:auto;
}
</style>
