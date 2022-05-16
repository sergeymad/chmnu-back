<template>
    <div>
        <heading class="mb-6">Supportchat</heading>

      <card>
        <div class="relative overflow-hidden overflow-x-auto">
          <table class="table w-full table-default">
            <thead>
            <tr>
              <th class="text-left">ID</th>
              <th class="text-left">Name</th>
              <th class="text-left">Last Message</th>
              <th class="text-left"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="user in users">
              <td>{{ user.id }}</td>
              <td>{{ user.name }}</td>
              <td>{{ user.lastMessage }}</td>
              <td>
                <a  :href="'supportchat/response/' + user.id" class="inline-flex cursor-pointer text-70 hover:text-primary mr-3 has-tooltip"  data-original-title="null"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="edit" role="presentation" class="fill-current"><path d="M4.3 10.3l10-10a1 1 0 0 1 1.4 0l4 4a1 1 0 0 1 0 1.4l-10 10a1 1 0 0 1-.7.3H5a1 1 0 0 1-1-1v-4a1 1 0 0 1 .3-.7zM6 14h2.59l9-9L15 2.41l-9 9V14zm10-2a1 1 0 0 1 2 0v6a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4c0-1.1.9-2 2-2h6a1 1 0 1 1 0 2H2v14h14v-6z"></path></svg></a>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </card>
    </div>
</template>

<script>
export default {
  data() {
    return {
      users: [],
    }
  },
  mounted() {
    this.getRoutes();
  },
  methods: {
    getRoutes() {
      Nova.request().get('/chatList').then(response => {
        this.users = response.data;
      });
    },
  },
}
</script>

<style>
/* Scoped Styles */
</style>
