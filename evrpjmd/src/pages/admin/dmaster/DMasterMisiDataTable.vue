<template>
  test
</template>

<script>
  import { usesUserStore } from '@/stores/UsersStore'
  export default {
		name: "DMasterMisiDataTable",
		created() {
      this.userStore = usesUserStore()
      this.fetchMisi()
    },
    props: {
      RpjmdVisiID: {
        type: String,
        RpjmdVisiID: null,
      },
    },
    data: () => ({

      //pinia
      userStore: null,
    }),
    methods: {
      async fetchMisi() {        
        if(this.RpjmdVisiID === null) {
          await this.$ajax
            .post('/rpjmd/visi', 
              {
                
              },
              {
                headers: {
                  Authorization: this.userStore.Token,
                },
              }
            )
            .then(({ data }) => {
              console.log(data)         
            })
        } else {
          await this.$ajax
            .get('/rpjmd/visi/' + this.RpjmdVisiID + '/misi', {
                headers: {
                  Authorization: this.userStore.Token,
                },
              }
            )
            .then(({ data }) => {
              console.log(data)         
            })
        }
      },
    },
  }
</script>