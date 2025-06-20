import { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import { Offer } from '@/types/offer';

interface OfferFilters {
  title?: string;
  offer_company?: string;
  offerer_type?: string;
  status?: string;
  average_rating_min?: number;
  created_at_from?: string;
  sort_field?: string;
  sort_direction?: string;
}

interface PaginationData {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export const useOffers = () => {
  const [offers, setOffers] = useState<Offer[]>([]);
  const [pagination, setPagination] = useState<PaginationData>({
    current_page: 1,
    last_page: 1,
    per_page: 20,
    total: 0,
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  
  // Separate Filter-Zustände für verzögerte und sofortige Filter
  const [delayedFilters, setDelayedFilters] = useState<OfferFilters>({
    title: '',
    offer_company: '',
    created_at_from: '',
  });
  
  const [immediateFilters, setImmediateFilters] = useState<OfferFilters>({
    offerer_type: '',
    status: '',
    average_rating_min: 0,
    sort_field: 'created_at',
    sort_direction: 'desc',
  });
  
  // Kombinierter Filter-Zustand für die API-Anfrage
  const [activeFilters, setActiveFilters] = useState<OfferFilters>({
    ...delayedFilters,
    ...immediateFilters
  });

  // Funktion zum Abrufen der Angebote
  const fetchOffers = useCallback(async (page: number = 1, resetResults: boolean = true) => {
    try {
      setLoading(true);
      setError(null);
      
      // Parameter für die Anfrage erstellen
      const params = new URLSearchParams();
      params.append('page', page.toString());
      
      // Filter hinzufügen
      Object.entries(activeFilters).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
          params.append(key, value.toString());
        }
      });
      
      const { data } = await axios.get(`/offers-fetch-more?${params.toString()}`);
      
      if (resetResults) {
        setOffers(data.offers);
      } else {
        setOffers(prev => [...prev, ...data.offers]);
      }
      
      setPagination(data.pagination);
    } catch (err) {
      setError('Fehler beim Laden der Angebote');
      console.error('Error fetching offers:', err);
    } finally {
      setLoading(false);
    }
  }, [activeFilters]);

  // Funktion zum Laden weiterer Angebote (für Infinite Scrolling)
  const loadMore = useCallback(() => {
    if (loading || !pagination || pagination.current_page >= pagination.last_page) return;
    fetchOffers(pagination.current_page + 1, false);
  }, [loading, pagination, fetchOffers]);

  // Funktion zum Aktualisieren der verzögerten Filter (ohne sofortige Anwendung)
  const updateDelayedFilters = useCallback((newFilters: Partial<OfferFilters>) => {
    setDelayedFilters(prev => {
      // Nur die verzögerten Filter aktualisieren
      const filteredUpdates: Partial<OfferFilters> = {};
      if ('title' in newFilters) filteredUpdates.title = newFilters.title;
      if ('offer_company' in newFilters) filteredUpdates.offer_company = newFilters.offer_company;
      if ('created_at_from' in newFilters) filteredUpdates.created_at_from = newFilters.created_at_from;
      
      return { ...prev, ...filteredUpdates };
    });
  }, []);
  
  // Funktion zum Aktualisieren der sofortigen Filter (mit sofortiger Anwendung)
  const updateImmediateFilters = useCallback((newFilters: Partial<OfferFilters>) => {
    setImmediateFilters(prev => {
      // Nur die sofortigen Filter aktualisieren
      const filteredUpdates: Partial<OfferFilters> = {};
      if ('offerer_type' in newFilters) filteredUpdates.offerer_type = newFilters.offerer_type;
      if ('status' in newFilters) filteredUpdates.status = newFilters.status;
      if ('average_rating_min' in newFilters) filteredUpdates.average_rating_min = newFilters.average_rating_min;
      if ('sort_field' in newFilters) filteredUpdates.sort_field = newFilters.sort_field;
      if ('sort_direction' in newFilters) filteredUpdates.sort_direction = newFilters.sort_direction;
      
      const newState = { ...prev, ...filteredUpdates };
      
      // Sofort aktive Filter aktualisieren
      setActiveFilters(current => ({ ...current, ...filteredUpdates }));
      
      return newState;
    });
  }, []);

  // Funktion zum Aktualisieren aller Filter (für Kompatibilität)
  const updateFilters = useCallback((newFilters: Partial<OfferFilters>) => {
    // Verzögerte Filter
    const delayedUpdates: Partial<OfferFilters> = {};
    if ('title' in newFilters) delayedUpdates.title = newFilters.title;
    if ('offer_company' in newFilters) delayedUpdates.offer_company = newFilters.offer_company;
    if ('created_at_from' in newFilters) delayedUpdates.created_at_from = newFilters.created_at_from;
    
    // Sofortige Filter
    const immediateUpdates: Partial<OfferFilters> = {};
    if ('offerer_type' in newFilters) immediateUpdates.offerer_type = newFilters.offerer_type;
    if ('status' in newFilters) immediateUpdates.status = newFilters.status;
    if ('average_rating_min' in newFilters) immediateUpdates.average_rating_min = newFilters.average_rating_min;
    if ('sort_field' in newFilters) immediateUpdates.sort_field = newFilters.sort_field;
    if ('sort_direction' in newFilters) immediateUpdates.sort_direction = newFilters.sort_direction;
    
    // Beide Filter-Typen aktualisieren
    if (Object.keys(delayedUpdates).length > 0) {
      setDelayedFilters(prev => ({ ...prev, ...delayedUpdates }));
    }
    
    if (Object.keys(immediateUpdates).length > 0) {
      setImmediateFilters(prev => ({ ...prev, ...immediateUpdates }));
      // Sofortige Filter direkt anwenden
      setActiveFilters(prev => ({ ...prev, ...immediateUpdates }));
    }
  }, []);

  // Funktion zum Anwenden der verzögerten Filter
  const applyFilters = useCallback(() => {
    setActiveFilters(current => ({ ...current, ...delayedFilters }));
  }, [delayedFilters]);

  // Initiales Laden der Angebote
  useEffect(() => {
    fetchOffers(1, true);
  }, [activeFilters, fetchOffers]);

  return { 
    offers, 
    pagination, 
    loading, 
    error, 
    loadMore, 
    updateFilters,
    updateDelayedFilters,
    updateImmediateFilters,
    applyFilters,
    hasMore: pagination && pagination.current_page < pagination.last_page,
    delayedFilters,
    immediateFilters,
    activeFilters
  };
};