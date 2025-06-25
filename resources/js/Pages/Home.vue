<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const challenges = ref([]);
const loading = ref(true);
const userProgress = ref({});
const leaderboard = ref([]);
const showLeaderboard = ref(false);
const selectedChallenge = ref(null);

onMounted(() => {
  fetch('/challenges/list')
    .then(res => res.json())
    .then(data => {
      challenges.value = data.challenges || [];
    })
    .finally(() => loading.value = false);
});

function joinChallenge(id) {
  fetch(`/challenges/${id}/join`, { method: 'POST' })
    .then(() => {
      userProgress.value[id] = 0;
    });
}

function updateProgress(id, progress) {
  fetch(`/challenges/${id}/progress`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ progress })
  }).then(() => {
    userProgress.value[id] = progress;
  });
}

function openLeaderboard(challenge) {
  selectedChallenge.value = challenge;
  fetch(`/challenges/${challenge.id}/leaderboard`)
    .then(res => res.json())
    .then(data => {
      leaderboard.value = data.leaderboard || [];
      showLeaderboard.value = true;
    });
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="container py-4">
      <h2 class="fw-bold mb-4"><i class="fa fa-trophy text-warning me-2"></i>Reading Challenges</h2>
      <div v-if="loading" class="text-center my-4"><span class="spinner-border"></span></div>
      <div v-else>
        <div v-if="challenges.length === 0" class="text-muted text-center">No active challenges.</div>
        <div v-else class="row g-3">
          <div v-for="c in challenges" :key="c.id" class="col-12">
            <div class="card shadow-sm mb-3">
              <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="fw-bold fs-5">{{ c.title }}</span>
                  <span class="badge bg-warning text-dark ms-2">Target: {{ c.target_books }} books</span>
                  <span class="ms-auto text-muted small">{{ c.start_date }} - {{ c.end_date }}</span>
                </div>
                <div class="mb-2">{{ c.description }}</div>
                <div v-if="userProgress[c.id] !== undefined">
                  <label>Progress:</label>
                  <input type="number" min="0" :max="c.target_books" v-model.number="userProgress[c.id]" @change="updateProgress(c.id, userProgress[c.id])" style="width: 80px;" />
                  / {{ c.target_books }}
                  <span v-if="userProgress[c.id] >= c.target_books" class="badge bg-success ms-2">Completed!</span>
                </div>
                <div v-else>
                  <button class="btn btn-outline-warning" @click="joinChallenge(c.id)"><i class="fa fa-flag-checkered"></i> Join Challenge</button>
                </div>
                <button class="btn btn-link p-0 ms-3" @click="openLeaderboard(c)"><i class="fa fa-list-ol"></i> View Leaderboard</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Leaderboard Modal -->
        <div v-if="showLeaderboard" class="modal-backdrop" @click.self="showLeaderboard = false" style="z-index: 2000; position: fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center;">
          <div class="modal-dialog" style="background: #fff; border-radius: 8px; max-width: 400px; width: 100%; box-shadow: 0 8px 32px rgba(0,0,0,0.18);">
            <div class="modal-content p-3">
              <div class="modal-header d-flex align-items-center justify-content-between">
                <h5 class="modal-title d-flex align-items-center gap-2">
                  <i class="fa fa-list-ol text-warning"></i>
                  Leaderboard - {{ selectedChallenge?.title }}
                </h5>
                <button type="button" class="btn-close" @click="showLeaderboard = false"></button>
              </div>
              <div class="modal-body">
                <table class="table table-sm">
                  <thead>
                    <tr><th>#</th><th>Name</th><th>Progress</th></tr>
                  </thead>
                  <tbody>
                    <tr v-for="(u, i) in leaderboard" :key="u.id">
                      <td>{{ i+1 }}</td>
                      <td>{{ u.name }}</td>
                      <td>{{ u.pivot_progress }}</td>
                    </tr>
                    <tr v-if="leaderboard.length === 0">
                      <td colspan="3" class="text-center text-muted">No participants yet.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>