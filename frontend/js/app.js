const app = {
    apiUrl: 'http://localhost:8000/index.php',
    filmsCache: null,
    sallesCache: null,
    lastSeances: null,
    seanceFilmId: 'all',
    seanceSalleId: 'all',
    formatDateTime(value) {
        if (!value) return '-';
        const date = new Date(value.replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) return value;
        return new Intl.DateTimeFormat('fr-FR', {
            dateStyle: 'medium',
            timeStyle: 'short'
        }).format(date);
    },
    async ensureFilmsLoaded() {
        if (this.filmsCache) return;
        const res = await fetch(`${this.apiUrl}?resource=films`);
        const data = await res.json();
        this.filmsCache = Array.isArray(data) ? data : [];
    },
    async ensureSallesLoaded() {
        if (this.sallesCache) return;
        const res = await fetch(`${this.apiUrl}?resource=salles`);
        const data = await res.json();
        this.sallesCache = Array.isArray(data) ? data : [];
    },
    setSeanceFilmFilter(value) {
        this.seanceFilmId = value;
        if (this.lastSeances) this.render('seances', this.lastSeances);
    },
    setSeanceSalleFilter(value) {
        this.seanceSalleId = value;
        if (this.lastSeances) this.render('seances', this.lastSeances);
    },
    resetSeanceFilters() {
        this.seanceFilmId = 'all';
        this.seanceSalleId = 'all';
        if (this.lastSeances) this.render('seances', this.lastSeances);
    },

    async load(resource) {
        if (resource === 'seances') {
            await this.ensureFilmsLoaded();
            await this.ensureSallesLoaded();
        }
        const response = await fetch(`${this.apiUrl}?resource=${resource}`);
        const data = await response.json();
        this.render(resource, data);
    },

    render(resource, data) {
        const container = document.getElementById('app-content');
        if (resource === 'home') {
            container.innerHTML = `
                <section class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-2">Bienvenue sur My Cinema</h2>
                    <p class="text-slate-600 mb-6">Gere les films, salles et seances depuis ce tableau de bord.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button onclick="app.load('films')" class="px-4 py-3 bg-slate-800 text-white rounded">Voir les films</button>
                        <button onclick="app.load('salles')" class="px-4 py-3 bg-slate-800 text-white rounded">Voir les salles</button>
                        <button onclick="app.load('seances')" class="px-4 py-3 bg-slate-800 text-white rounded">Voir le planning</button>
                    </div>
                </section>`;
            return;
        }
        if (resource === 'films') {
            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = '<h2 class="text-2xl mb-4 font-bold">Liste des Films</h2><p>Aucun film trouvé.</p>';
                return;
            }
            container.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">Liste des Films</h2>
                    <button onclick="app.load('films')" class="text-sm px-3 py-1 bg-slate-800 text-white rounded">Actualiser</button>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50"><tr><th class="p-4 text-left">Titre</th><th class="p-4">Genre</th><th class="p-4">Duree</th><th class="p-4">Action</th></tr></thead>
                        <tbody>${data.map(m => `
                            <tr class="border-t">
                                <td class="p-4">${m.title}</td>
                                <td class="p-4">${m.genre ?? '-'}</td>
                                <td class="p-4">${m.duration ?? '-'} min</td>
                                <td class="p-4 text-center">
                                    <button onclick="app.delete('films', ${m.id})" class="text-red-500">Supprimer</button>
                                </td>
                            </tr>`).join('')}
                        </tbody>
                    </table>
                </div>`;
            return;
        }
        if (resource === 'salles') {
            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = '<h2 class="text-2xl mb-4 font-bold">Liste des Salles</h2><p>Aucune salle trouvée.</p>';
                return;
            }
            container.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">Liste des Salles</h2>
                    <button onclick="app.load('salles')" class="text-sm px-3 py-1 bg-slate-800 text-white rounded">Actualiser</button>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50"><tr><th class="p-4 text-left">Nom</th><th class="p-4">Type</th><th class="p-4">Capacite</th><th class="p-4">Action</th></tr></thead>
                        <tbody>${data.map(s => `
                            <tr class="border-t">
                                <td class="p-4">${s.name}</td>
                                <td class="p-4">${s.type ?? '-'}</td>
                                <td class="p-4">${s.capacity ?? '-'}</td>
                                <td class="p-4 text-center">
                                    <button onclick="app.delete('salles', ${s.id})" class="text-red-500">Supprimer</button>
                                </td>
                            </tr>`).join('')}
                        </tbody>
                    </table>
                </div>`;
            return;
        }
        if (resource === 'seances') {
            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = '<h2 class="text-2xl mb-4 font-bold">Planning des Seances</h2><p>Aucune seance planifiee.</p>';
                return;
            }
            this.lastSeances = data;
            const filmOptions = (this.filmsCache || []).map(f =>
                `<option value="${f.id}" ${String(f.id) === String(this.seanceFilmId) ? 'selected' : ''}>${f.title}</option>`
            ).join('');
            const salleOptions = (this.sallesCache || []).map(s =>
                `<option value="${s.id}" ${String(s.id) === String(this.seanceSalleId) ? 'selected' : ''}>${s.name}</option>`
            ).join('');
            const filtered = data.filter(se => {
                const matchFilm = this.seanceFilmId === 'all' || String(se.movie_id) === String(this.seanceFilmId);
                const matchSalle = this.seanceSalleId === 'all' || String(se.room_id) === String(this.seanceSalleId);
                return matchFilm && matchSalle;
            });
            container.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">Planning des Seances</h2>
                    <div class="flex flex-wrap items-center gap-3">
                        <label class="text-sm text-slate-600">Film</label>
                        <select onchange="app.setSeanceFilmFilter(this.value)" class="text-sm border rounded px-2 py-1">
                            <option value="all">Tous les films</option>
                            ${filmOptions}
                        </select>
                        <label class="text-sm text-slate-600">Salle</label>
                        <select onchange="app.setSeanceSalleFilter(this.value)" class="text-sm border rounded px-2 py-1">
                            <option value="all">Toutes les salles</option>
                            ${salleOptions}
                        </select>
                        <button onclick="app.resetSeanceFilters()" class="text-sm px-3 py-1 border rounded">Reinitialiser</button>
                        <button onclick="app.load('seances')" class="text-sm px-3 py-1 bg-slate-800 text-white rounded">Actualiser</button>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50"><tr><th class="p-4 text-left">Film</th><th class="p-4">Salle</th><th class="p-4">Debut</th><th class="p-4">Fin</th></tr></thead>
                        <tbody>${filtered.map(se => `
                            <tr class="border-t">
                                <td class="p-4">${se.titre_film ?? '-'}</td>
                                <td class="p-4">${se.nom_salle ?? '-'}</td>
                                <td class="p-4">${this.formatDateTime(se.start_at)}</td>
                                <td class="p-4">${this.formatDateTime(se.end_at)}</td>
                            </tr>`).join('')}
                        </tbody>
                    </table>
                </div>`;
        }
    },

    async delete(resource, id) {
        if (!confirm('Confirmer la suppression ?')) return;
        const res = await fetch(`${this.apiUrl}?resource=${resource}&id=${id}`, { method: 'DELETE' });
        const result = await res.json();
        if (result.error) {
            alert(result.error);
            return;
        }
        this.load(resource);
    }
};

window.onload = () => app.render('home');