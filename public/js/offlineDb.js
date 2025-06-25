import Dexie from '/assets/js/dexie.mjs';

const db = new Dexie('HmsOfflineDB');

db.version(1).stores({
    patients: 'id,national_id,medical_id,first_name',
    pendingSync: '++id,action,timestamp'
});

export async function saveOfflinePatient(formData) {
    const data = Object.fromEntries(formData);
    
    await db.transaction('rw', db.patients, db.pendingSync, async () => {
        // Save to pending sync queue
        await db.pendingSync.add({
            action: 'create',
            data: data,
            timestamp: new Date()
        });

        // Save to local patients store
        await db.patients.add({
            ...data,
            id: Date.now(), // Temporary ID
            created_at: new Date(),
            updated_at: new Date()
        });
    });
}

export async function syncOfflineData() {
    const pending = await db.pendingSync.toArray();
    
    for (const action of pending) {
        try {
            const response = await fetch('/patients', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(action.data)
            });

            if (response.ok) {
                await db.pendingSync.delete(action.id);
            }
        } catch (error) {
            console.error('Sync failed:', error);
        }
    }
}