Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'supportchat',
      path: '/supportchat',
      component: require('./components/Tool').default,
    },
    {
      name: 'chat',
      path: '/supportchat/response/:id',
      component: require('./components/Chat').default,
      props: (route) => {
        const chatId = Number.parseInt(route.params.id, 10)
        if (Number.isNaN(chatId)) {
          return 0
        }
        return { chatId }
      }
    },
  ])
})
