export default {
  
  data() {
    return {

      en: {
        'Projekte': 'Projects',
        'Projekt bearbeiten': 'Edit project',
        'Projekt erstellen': 'Create project',
        'Projekt erfasst': 'Project created',
        'Änderungen gespeichert': 'Changes saved',
        'Einladung versendet': 'Invitation sent',
        'Vertec Id' : 'Vertec Id',
        'Nummer': 'Number',
        'Name': 'Name',
        'Vorname': 'Firstname',
        'Telefon': 'Phone',
        'E-Mail': 'Email',
        'Geschlecht': 'Gender',
        'Sprache': 'Language',
        'Kunde': 'Client',
        'Kunden': 'Clients',
        'Ort': 'City',
        'Zugangsdaten': 'Access',
        'Passwort': 'Password',
        'Passwort wiederholen': 'Repeat password',
        'Rolle': 'Role',
        'Benutzer erfasst': 'User created',
        'Passwörter stimmen nicht überein': 'Passwords do not match',
        'Passwort (min. 8 Zeichen)': 'Password (min. 8 characters)',
        'Passwort vorschlagen': 'Suggest password',
        'Benutzer': 'Users',
        'Benutzer bearbeiten': 'Edit user',
        'Benutzer hinzufügen': 'Add user',
        'Projektleiter': 'Project Lead',
        'Bitte wählen...': 'Please choose...',
        'Projektstart': 'Project Start',
        'Abgabetermin': 'Deadline',
        'Hauptkunde': 'Main client',
        'Weitere Kunden': 'Other clients',
        'Status': 'State',
        'Farbe': 'Color',
        'Speichern': 'Save',
        'Zurück': 'Back',
        'Pflichtfeld': 'Required',
        'Kunde bearbeiten': 'Edit client',
        'Kunde hinzufügen': 'Add client',
        'Firma erfasst': 'Company crated',
        'Senden': 'Send message',
        'Empfänger': 'Receivers',
        'Alle': 'all',
        'Betreff': 'Subject',
        'Mitteilung': 'Message',
        'Dateien': 'Files',
        'Datei auswählen oder hierhin ziehen': 'Select or drag and drop files',
        'max. Grösse 250 MB': 'max. 250 MB',
        'Benutzer für': 'Users of',
        'Bitte löschen bestätigen': 'Please confirm',
        'Es sind noch keine Daten vorhanden': 'No data yet',
        'Pendent': 'Pending',
        'Admin': 'Admin',
        'gelöschter Benutzer': 'deleted user',
        'an': 'to',
        'um': 'at',
        'Mehr anzeigen': 'Show more',
        'Weniger anzeigen': 'Show less',
        'Download als ZIP': 'Download ZIP-archive',
        'Nachricht gelöscht durch': 'Message deleted by',
        'Nachricht löschen': 'Delete message',
        'Erstellen': 'Create',
        'Neue Nachricht': 'New message',
        'Profil bearbeiten': 'Edit profile',
        'Private Nachricht?': 'Private message?',
        'Private Nachrichten': 'Private messages',
        'Anhänge Filtern': 'Filter attachements',
        'Aktionen': 'Actions',
        'Antworten': 'Reply',
        'Empfänger auswählen': 'Select recipients',
        'Ja': 'Yes',
        'Nein': 'No',
        'Neue Nachricht': 'New message',
        'Schliessen': 'close',
        'Abbrechen': 'cancel',
        'Zugriffsrechte': 'Access',
        'archivierte Projekte': 'show archive',
        'aktive Projekte': 'show active projects',
        'Switch language to': 'Sprache wechseln',
        'Bitte Empfänger auswählen': 'Please seledt at least one recipient',
        'Es wurde kein kundenseitiger Empfänger ausgewählt. Trotzdem fortfahren?': 'No customer-side recipient was selected. Continue anyway?',
      },

      defaultLang: 'de',
    }
  },

  methods: {

    translate(key) {
      const locale = this._locale();
      if (locale == this.defaultLang || this[locale][key] == undefined) {
        return key;
      }
      return this[locale][key];
    },

    _locale() {
      return this.$store.state.user ? this.$store.state.user.language : this.defaultLang;
    },
  }
};