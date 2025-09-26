import React from 'react';
import './RoleSelectorModal.css';

interface RoleSelectorModalProps {
  roles: string[];
  onSelect: (role: string) => void;
  onCancel: () => void;
}

const roleDisplayMap: Record<string, string> = {
  admin: 'Administrator',
  principal: 'Principal',
  hod: 'Head of Department',
  faculty: 'Faculty',
  student: 'Student'
};

const RoleSelectorModal: React.FC<RoleSelectorModalProps> = ({ roles, onSelect, onCancel }) => {
  return (
    <div className="role-modal-backdrop">
      <div className="role-modal">
        <h2>Select Portal</h2>
        <p>Your account has multiple roles. Choose which portal to enter now. You can switch later after logout.</p>
        <div className="role-list">
          {roles.map(r => (
            <button key={r} className="role-btn" onClick={() => onSelect(r)}>
              {roleDisplayMap[r] || r}
            </button>
          ))}
        </div>
        <button className="cancel-btn" onClick={onCancel}>Cancel</button>
      </div>
    </div>
  );
};

export default RoleSelectorModal;
