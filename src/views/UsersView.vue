<template>
  
  <section class="users">
    <div class="my-4">
        <div class="form-row">
          <div v-if="AddNew" class="modal_fade" id="Users" tabindex="-1" role="dialog" aria-labelledby="editLabel" >
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-dialog modal-lg" role="document">
                  <h5 class="modal-title text-center my-4 text-nowrap bg-body-secondary border text-warning-emphasis" id="editLabel_1" v-show="!editModal">Add New Member</h5>
                  <h5 class="modal-title my-4 text-nowrap bg-body-secondary border text-warning-emphasis" id="editLabel_2 " v-show="!addModal">Update Users informations</h5>
                <div class="modal-content">
                  <form >
                    <div class="modal-body mx-3">
                      <div class="form-row ">
                        <div class="form-group col-md-6 mx-auto">
                          <label for="name">Name</label>
                          <input autocomplete="off" type="text" name="name" class="form-control" id="name" placeholder="Name" v-model="UsersData.name">
                        </div>
                        <div class="form-group col-md-6 mx-auto">
                          <label for="email">Email</label>
                          <input autocomplete="off" type="email" name="email" class="form-control" id="email" placeholder="Email" v-model="UsersData.email" >
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="form-group col-md-6 mx-auto ">
                          <label for="name">Highest Education</label>
                          <select name="eduction" class="form-control" id="education" v-model="UsersData.education">
                            <option v-for="ed in Education" :value="ed">
                              {{ed}}
                            </option>
                          </select>
                        </div>
                      
                      </div>
                      
                    </div>
                    <div class="modal_footer">
                      
                      <button  type="button" class="btn btn-success mx-3 my-3 border border-dark" @click="AddUsers"> Create a New User</button>
                      <button  type="button" class="btn btn-warning mx-5 my-3 border border-danger"  @click="editUsers(UsersData); AddNew = false" v-show="!addModal"> User to Edit </button>
                      <button @click="AddNew = false" class="btn btn-dark my-3 border border-danger" >Close</button>

                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-12 col-sm-12">
            <button @click="AddNew = true" class="btn btn-primary btn-lg float-right border border-dark" >Add User</button>
        </div>
    </div>


    <div class="form-row mx-3">
      <div class="col-md-12 mt-4">
          <div class="table-responsive">
          <table class="table table-striped table-hover table-bordered border border-dark">
              <thead class="text-center">
                <tr>
                  <th style="background-color: #00FF00;"><strong >ID</strong></th>
                  <th style="background-color: #00FF00;"><strong>NAME</strong></th>
                  <th style="background-color: #00FF00;"><strong>EMAIL</strong></th>
                  <th style="background-color: #00FF00;"><strong>EDUCATION LEVEL</strong></th>
                  <th style="background-color: #00FF00;"><strong>ACTION</strong></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="data in Users">
                  <td class="text-center" style="background-color: #FFFF00;"><strong>{{data.id}}</strong></td>
                  <td>{{data.name}}</td>
                  <td>{{data.email}}</td>
                  <td>{{data.education}}</td>
                  <td class="text-center"> 
                    <button class="btn btn-outline-danger btn-sm" @click="deleteUsers(data.id)">Delete</button>
                    <button class="btn btn-warning btn-md mx-5 border border-danger"  @click="editUsers(data)">  Edit </button>
                    <button class="btn btn-outline-success btn-sm" @click="getUser( data.id, data.name, data.email, data.education)">Information</button>
                  </td>
                </tr>
              </tbody>
          </table>
          </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from "axios";
import jQuery from 'jquery';

export default {
  name: "users",
  data() {
    return {
      AddNew: false,
      modal:null,
      editModal:false,
      addModal:true,
      Education:["High school", "Bachelor''s degree","Master''s degree","Doctorate"],
      UsersData:{
        id: null,
        name:null,
        email:null,
        education:null,
        gender:null,
      },
      Users:[],
    };
  },
  created(){
  this.getUsers();
  },
  methods:{ 
    AddUsers(){
      let data= new FormData();
      data.append("name" ,this.UsersData.name);
      data.append("email" ,this.UsersData.email);
      data.append("education" ,this.UsersData.education);
      axios.post('http://localhost:8000?action=addusers',data).then((res)=>{
        if(res.data.error){
          alert("Error");
        }else{
          jQuery("#Users").hide();
          this.getUsers();
          alert(res.data.message);
          Object.assign(this.$data, this.$options.data());
        }
      }).catch((err)=>{
        console.log(err);
      })

    },
    getUsers(){
      axios.get('http://localhost:8000?action=getusersinfo').then((res)=>{
        console.log(res.data.user_Data);
        this.Users=res.data.user_Data;
      }).catch((err)=>{
        console.log(err);
      })
    },

    async getUser(id, name, email, education){
      try {
        alert(`\nID =>                  ${id}\n\nNAME =>            ${name}\n\nEMAIL =>            ${email}\n\nEDUCATION  =>  ${education}`);

      } catch (error) {
          console.error("Error al eliminar:", error);
      }
    },

    editUsers(userid){
      this.addModal=false;
      this.editModal=true;
      this.AddNew= true,
      this.UsersData =userid;
      jQuery("#Users").show();

      let data= new FormData();
      data.set("id" ,this.UsersData.id);
      data.set("name" ,this.UsersData.name);
      data.set("email" ,this.UsersData.email);
      data.set("education" ,this.UsersData.education);
      
      axios.post('http://localhost:8000?action=editusers',data).then((res)=>{
      
        if(res.data.error){
          alert("Error");
        }
    
      }).catch((err)=>{
        console.log(err);
      })

    },

    async deleteUsers(id) {
      try {
        const response = await axios.post('http://localhost:8000?action=deleteusers', { id: id });
     
        this.getUsers();
        alert('ID: ' + id + '\nwas succesfully deleted!');

      } catch (error) {
          console.error("Error al eliminar:", error);
      }
    }

  },
};
</script>

<style>
      body {
        background-image:url('/src/assets/madera.jpg');
      
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
        animation: 8s infinite pulse;
        animation-composition: add;
      }
      
      @keyframes pulse {
    0% {
      filter: hue-rotate(0deg) opacity(0.8);
    }
    50% {
      filter: hue-rotate(180deg) opacity(0.9);
    }
    100% {
      filter: hue-rotate(360deg) opacity(0.99);
    }
  }
    </style>